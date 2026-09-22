# ADR-0006: Checkout como invitado con registro diferido

- **Status**: Accepted
- **Date**: 2026-09-21
- **Deciders**: JuanMunozBouchard

## Context

Obligar a crear una cuenta antes de comprar es una de las principales causas de abandono
en venta de entradas. Pero el sistema necesita, sí o sí, una dirección de correo: es por
donde viajan la confirmación de compra y las entradas con QR.

Además, el histórico de compras tiene que sobrevivir al registro: quien compró tres veces
como invitado y después se registra debe ver esas tres compras.

## Decision

1. **Comprar no requiere cuenta.** El checkout pide un email obligatorio y crea (o
   reutiliza) un registro en `customers`.
2. **`customers.password` es nullable.** `NULL` significa invitado: existe como entidad
   para colgarle órdenes y tickets, pero no puede iniciar sesión.
3. **`customers.email` es único** y funciona como identidad del comprador. Un invitado
   recurrente reutiliza su registro; no se duplica.
4. **`customers.registered_at` marca el alta formal.** El registro posterior setea
   `password` y `registered_at` sobre el mismo registro, de modo que el histórico queda
   vinculado sin migrar datos.
5. **La liberación de órdenes vencidas queda fuera de alcance** por ahora: el cupo se
   reserva al crear la orden y no se libera automáticamente.

## Consequences

### Positive

- Menor fricción en la compra, que es el objetivo del negocio.
- El histórico es continuo: registrarse no crea una identidad nueva.
- Un solo modelo `Customer` para ambos casos; el resto del dominio no distingue entre
  invitado y registrado.

### Negative

- **El email no está verificado al comprar.** Un typo manda las entradas a la nada, y
  alguien podría usar el email de otra persona. Mitigación futura: verificación por enlace
  antes de emitir, o reenvío desde el panel.
- **Un tercero podría "ocupar" un email** comprando como invitado con la dirección de otro;
  al registrarse el dueño legítimo hereda compras que no hizo. Aceptable en este contexto,
  pero conviene exigir verificación de email en el alta.
- **`password` nullable** obliga a cuidar el guard de clientes: nunca debe autenticar a un
  registro con contraseña nula.
- **BR-12b es deuda técnica explícita**: una orden impaga retiene cupo para siempre. Con
  volumen, el aforo se agota con reservas fantasma.

### Follow-up

- Definir el guard de `customers` y garantizar que rechace `password IS NULL`.
- Decidir si se exige verificación de email antes de emitir las entradas.
- Implementar, cuando haga falta, el job que expire órdenes y devuelva `sold_count`
  (levanta BR-12b).

## Alternatives considered

| Alternativa | Por qué se descartó |
|---|---|
| Registro obligatorio antes de comprar | Fricción alta y abandono de carrito; el negocio pidió explícitamente lo contrario |
| Guardar el email solo en la orden, sin `customer` | Se pierde el histórico por comprador y hay que deducirlo con un `group by email` |
| Tabla `guests` separada de `customers` | Al registrarse habría que migrar órdenes y tickets entre tablas; el nullable evita esa mudanza |
