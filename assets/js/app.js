
const formContact = document.querySelector('form[name="form_contact"]')

const eventListeners = () => {
   formContact.addEventListener('submit', enviarFormulario)
}

function enviarFormulario(e) {
   e.preventDefault();
   const nombre = e.target.querySelector('input[name="nombre"]')
   const correo = e.target.querySelector('input[name="correo"]')

   if (document.querySelector('.error')) {
       formContact.removeChild(document.querySelector('.error'))
   }

   if (nombre.value.length <= 0 || correo.value.length <= 0) {
       const error = document.createElement('P');
       error.classList.add('error', 'text-danger', 'p-2', 'text-center', 'border-danger')
       error.textContent = 'Todos los campos son obligatorios'
       formContact.appendChild(error)
       return
   }

   //Enviar el correo
   e.target.submit();
}

eventListeners()
