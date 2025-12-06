export class EditForm extends HTMLFormElement {
	static get name() { return "edit-form-component"; }

	constructor() {
		super();
	}

	/**
	 * Creates an instance of `EditForm`
	 *
	 * @static
	 * @returns {EditForm}
	 */
	static create() {
		/** @type {EditForm} */
		const editForm = document.createElement("form", { is: EditForm.name });
		return editForm;
	}

	/**
	 * Convert the form fields into a plain object
	 * @returns {Object}
	 */
	toJSON() {
		const formData = new FormData(this);
		const obj = {};

		formData.forEach((value, key) => {
			// Handle multiple inputs with same name (checkboxes, multi-select)
			if (obj[key]) {
				if (Array.isArray(obj[key])) {
					obj[key].push(value);
				} else {
					obj[key] = [obj[key], value];
				}
			} else {
				obj[key] = value;
			}
		});

		return obj;
	}
}

customElements.define(EditForm.name, EditForm, { extends: "form" });