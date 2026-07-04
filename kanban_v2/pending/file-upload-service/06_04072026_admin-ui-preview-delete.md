# 06 — Admin UI: preview de archivo en edit + acción delete

**Track:** file-upload-service
**Proyecto:** core (Common)
**Prioridad:** low
**Estado:** pending

## Objetivo
- Blade components `file-preview` y `file-delete`
- Preview de imagen/archivo en la vista edit
- Acción delete que use `FileUploaderInterface::delete(FileDeleteCommand)`
- Contemplar URLs temporales firmadas (`Storage::temporaryUrl()`) para discos privados en S3

## Dependencias
- SF-03 y SF-04
