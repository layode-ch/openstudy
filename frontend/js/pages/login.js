import { Alert, EditForm } from "../components/index.js";
import APIClient, { APIError } from "../modules/apiClient.js";
import Page from "../modules/page.js";
import app from "../script.js";

export default class Login extends Page {
	/** @type {EditForm} */
	form;
	async init() {
		this.errors = document.querySelector("#errors");
		this.form = document.querySelector(`form[is="${EditForm.name}"]`);
		this.form.addEventListener("submit", this.#formOnSubmit.bind(this));
	}

	
	/**
	 * Description placeholder
	 *
	 * @param {SubmitEvent} e 
	 */
	async #formOnSubmit(e) {
		e.preventDefault();
		const response = await APIClient.login(this.form);

		if (response instanceof APIError) {
			this.#displayErrors(response.errors);
		}
		else {
			APIClient.token = response.token;
			app.pageManager.changePage("/");
		}
	}

	#displayErrors(errors) {
		this.errors.innerHTML = "";
		for (const error of errors) {
			const alert = Alert.create(error, "error");
			alert.classList.add("mb-3");
			this.errors.append(alert);
		}
	}
} 