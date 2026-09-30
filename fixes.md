# Fixes

Documento de correcciones del desarrollo.

Este archivo contiene errores o comportamientos incorrectos que deben ser
corregidos en el sistema.

## Instrucciones para el agente

- Revisar cada corrección de forma independiente.
- Antes de modificar código, revisar el funcionamiento actual y el contexto
  relacionado con la corrección.
- No modificar funcionalidades que no estén relacionadas con la corrección.
- Mantener la arquitectura y patrones existentes del proyecto, salvo que sea
  necesario modificarlos para solucionar el problema.
- Después de cada corrección, verificar que el comportamiento esperado se cumpla.
- No eliminar funcionalidades existentes sin indicarlo explícitamente.
- Si una corrección requiere una decisión que no está especificada aquí,
  detenerse y solicitar aclaración.
- Marcar una corrección como completada únicamente después de verificarla.
- Modificar únicamente lo necesario para corregir el comportamiento mencionado en cada corrección.

---

## FIX-001 — Error de hover sobre los inputs

**Estado:** Pendiente  
**Página:** `/instalacion/paso/2`  
**Sección:** Instalación

### Problema

Cuando se paso sobre los inputs para llenar la información de instalación, el punto desaparece. Esto puedo traer inconformidad del usuario al no saber donde esta el punto y no saber si se debe hacer clic sobre un input o no.

### Comportamiento actual

Al apuntar un input en al sección de instalación el punto del mouse se desaparece, es como situviera algun afeceto que le cambio su forma. Esto sucede en todo los inputs del proceso de instalación sin excepcion.


### Comportamiento esperado

Al pasar el mouser sobre el input deberia cambiar a "defaul" para que se sepa que debe escribir, o si es un select cambiar a "pointer" para entender que debe hacer una selección.

### Condiciones / reglas

- Que no desaparesca el puntero del mouse al pasar sobre un input.
- Que cambie a la forma que mejor se relaciones según el tipo de input sobre el que esta pasando.

---

