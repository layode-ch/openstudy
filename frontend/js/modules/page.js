/**
 * Classe représentant une page.
 */
export default class Page {
	
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
}