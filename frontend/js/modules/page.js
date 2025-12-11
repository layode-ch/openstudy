/**
 * Classe représentant une page.
 */
export default class Page {
	/** @type {HTMLElement} */
	main = null;
	/**
	 * @constructor
	 * @param {HTMLTemplateElement|string} template 
	 */
	constructor(template) {
		if (template instanceof HTMLTemplateElement)
			this.template = template;
		else
			this.template = document.querySelector(`${template}`);
	}

	/**
	 * Initialise la page.
	 * @async
	 * @returns {Promise<void>} 
	 */
	async init() { }



	displayErrors(errors) {
		this.errors.innerHTML = "";
		for (const error of errors) {
			const alert = Alert.create(error, "error");
			alert.classList.add("mb-3");
			this.errors.append(alert);
		}
	}
}