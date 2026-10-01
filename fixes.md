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

**Estado:** Implementado, pendiente de validación  
**Página:** `/instalacion/paso/2`  
**Sección:** Instalación

### Corrección aplicada

`public/css/install/install.css`: `.field input`/`.field select`/`.field textarea` no
tenían `cursor` definido. Se añadió `cursor: text` a `.field input`/`.field textarea`
(texto, email, teléfono, contraseña) y `cursor: pointer` a `.field select`. Afecta a
todos los pasos del asistente (comparten esta misma hoja de estilos), sin tocar
`.theme-card`, `.radio` ni `.upload__zone`, que ya tenían su propio `cursor: pointer`.

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

## FIX-002 — Valor de moneda y time zone

**Estado:** Pendiente  
**Página:** `/instalacion/paso/4`  
**Sección:** Instalación

### Problema

El valor de moneda esta en Euros, y el horario por defecto esta en madrid españa.

### Comportamiento actual

- En la sección de instalación cuando se va a agregar un tipo de plan aparece la moneda de EUROS, y no me permite poner por ejemplo $120.000 en pesos colombianos, porque tambien tiene un limte de caracteres al parecer.

- En el formato general las horas se estan guardando en el horario de madrid, porque en el .env esta ese formato de time zone.


### Comportamiento esperado

- Que la moneda sea por defecto en COP, y me permita ingresar solores de 6 o 7 cifras.
- El formato de horario debe ser por defecto el de bogota.

### Condiciones / reglas

- Que pueda agregar los valores que necesita el usuario para poner el precio de los servicios en COP
- Que todos los horarios se guarden con el timezone de bogota.

---

