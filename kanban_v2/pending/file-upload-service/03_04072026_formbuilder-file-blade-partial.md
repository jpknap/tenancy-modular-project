# 03 — FormBuilder::file() + Blade partial _file-input

**Track:** file-upload-service
**Proyecto:** core (Common)
**Prioridad:** medium
**Estado:** pending

## Objetivo
- `FormBuilder::file(string $name, string $label, array $options = []): self`
- Blade partial `_file-input.blade.php`
- Cuando el form tiene al menos un campo file, emitir `enctype="multipart/form-data"` automáticamente

## Dependencias
- Backend listo: SF-01, SF-02, SF-05 (done el 04/07/2026)
