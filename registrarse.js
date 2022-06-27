"use strict";
const boton = document.getElementById("buttonRegistro");

boton.addEventListener("click", registrarUsuario);
const inputName = document.getElementById("nombre");
const inputApellidos = document.getElementById("apellidos");
const inputEmail = document.getElementById("email");
const inputC_Autonoma = document.getElementById("c_autonoma");
const inputAños = document.getElementById("años");
const inputPhone = document.getElementById("telefono");
const inputCheckbox1 = document.getElementById("checkdeportes");
const inputCheckbox2 = document.getElementById("checkeconomia");
const inputCheckbox3 = document.getElementById("checkciencia");

// setTimeout(checkRecaptcha, 2000);
grecaptcha.ready(function () {
	// do request for recaptcha token
	// response is promise with passed token
	grecaptcha
		.execute("6LelHlMgAAAAAO9gx2FNBuWbysQlyklgmF0PWpXW", {
			action: "validate_captcha",
		})
		.then(function (token) {
			// add token value to form
			document.getElementById("g-recaptcha-response").value = token;
			checkRecaptcha();
		});
});
function checkRecaptcha() {
	let inputCaptcha_valor = document.getElementById(
		"g-recaptcha-response"
	).value;
	console.log(inputCaptcha_valor);
	if (inputCaptcha_valor != "") {
		boton.disabled = false;
		return true;
	}
	return false;
}
function registrarUsuario() {
	let inputName_valor = inputName.value;
	let inputApellidos_valor = inputApellidos.value;
	let inputEmail_valor = inputEmail.value;
	let inputC_Autonoma_valor = inputC_Autonoma.value;
	let inputAños_valor = inputAños.value;
	let inputPhone_valor = inputPhone.value;
	let inputCheckbox1_valor = inputCheckbox1.value;
	let inputCheckbox2_valor = inputCheckbox2.value;
	let inputCheckbox3_valor = inputCheckbox3.value;

	let nameBoolean = true;
	let apellidosBoolean = true;
	let emailBoolean = true;
	let c_autonomaBoolean = true;
	let añosBoolean = true;
	let phoneBoolean = true;
	let checkbox1Boolean = true;
	let checkbox2Boolean = true;
	let checkbox3Boolean = true;

	if ((inputName_valor == "") & !isNaN(inputName_valor)) {
		nameBoolean = false;
	}
	if (inputName_valor.length < 2) {
		nameBoolean = false;
	}
	if ((inputApellidos_valor == "") & !isNaN(inputApellidos_valor)) {
		apellidosBoolean = false;
	}
	if (inputApellidos_valor.length < 2) {
		apellidosBoolean = false;
	}
	if ((inputEmail_valor == "") & !isNaN(inputEmail_valor)) {
		emailBoolean = false;
	}
	if ((inputC_Autonoma_valor == "") & !isNaN(inputC_Autonoma_valor)) {
		c_autonomaBoolean = false;
	}
	if ((inputAños_valor == "") & isNaN(inputAños_valor)) {
		añosBoolean = false;
	}
	if ((inputPhone_valor == "") & isNaN(inputPhone_valor)) {
		phoneBoolean = false;
	}
	if ((inputCheckbox1_valor == "") & isNaN(inputCheckbox1_valor)) {
		checkbox1Boolean = false;
	}
	if ((inputCheckbox2_valor == "") & isNaN(inputCheckbox2_valor)) {
		checkbox2Boolean = false;
	}
	if ((inputCheckbox3_valor == "") & isNaN(inputCheckbox3_valor)) {
		checkbox3Boolean = false;
	}
	/*
if (nameBoolean && emailBoolean && phoneBoolean && passwordBoolean && checkRecaptcha()) {
		//insert DB
	}
*/
	$.ajax({
		url: "./registrarse.php",
		type: "POST",
		data: {
			api: "checkEmail",
			name: inputName_valor,
			apellidos: inputApellidos_valor,
			email: inputEmail_valor,
			c_autonoma: inputC_Autonoma_valor,
			años: inputAños_valor,
			phone: inputPhone_valor,
			checkbox1: inputCheckbox1_valor,
			checkbox2: inputCheckbox2_valor,
			checkbox3: inputCheckbox3_valor,
			captcha: document.getElementById("g-recaptcha-response").value,
		},
		dataType: "json",
		success: function (response) {
			if (response == 0) {
				console.warn(response);
			} else {
				console.log(response);
				if ("error" in response) {
					console.warn("ERROR");
					emailBoolean = false;
				} else {
					console.warn("OK");
					emailBoolean = true;
				}
				coloresCampos(
					nameBoolean,
					apellidosBoolean,
					emailBoolean,
					c_autonomaBoolean,
					añosBoolean,
					phoneBoolean
				);
			}
		},
		error: function (error) {
			console.warn("ERROR: ");
			console.warn(error);
			emailBoolean = false;
			coloresCampos(
				nameBoolean,
				apellidosBoolean,
				emailBoolean,
				c_autonomaBoolean,
				añosBoolean,
				phoneBoolean
			);
		},
	});
}
function coloresCampos(
	nameBoolean,
	apellidosBoolean,
	emailBoolean,
	c_autonomaBoolean,
	añosBoolean,
	phoneBoolean
) {
	if (nameBoolean) {
		inputName.classList.remove("inputError");
		inputName.classList.add("inputSuccess");
	} else {
		inputName.classList.remove("inputSuccess");
		inputName.classList.add("inputError");
	}
	if (apellidosBoolean) {
		inputApellidos.classList.remove("inputError");
		inputApellidos.classList.add("inputSuccess");
	} else {
		inputApellidos.classList.remove("inputSuccess");
		inputApellidos.classList.add("inputError");
	}
	if (emailBoolean) {
		inputEmail.classList.remove("inputSuccess");
		inputEmail.classList.add("inputError");
	} else {
		inputEmail.classList.remove("inputError");
		inputEmail.classList.add("inputSuccess");
	}
	if (c_autonomaBoolean) {
		inputC_Autonoma.classList.remove("inputError");
		inputC_Autonoma.classList.add("inputSuccess");
	} else {
		inputC_Autonoma.classList.remove("inputSuccess");
		inputC_Autonoma.classList.add("inputError");
	}
	if (añosBoolean) {
		inputAños.classList.remove("inputError");
		inputAños.classList.add("inputSuccess");
	} else {
		inputAños.classList.remove("inputSuccess");
		inputAños.classList.add("inputError");
	}
	if (phoneBoolean) {
		inputPhone.classList.remove("inputError");
		inputPhone.classList.add("inputSuccess");
	} else {
		inputPhone.classList.remove("inputSuccess");
		inputPhone.classList.add("inputError");
	}
}
