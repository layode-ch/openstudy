import APIClient, { APIError } from "../modules/apiClient.js";
import app from "../script.js";
import { EditForm } from "./EditForm.js";
import { Toast } from "./Toast.js";

export class TermForm extends EditForm {

	static get name() { return "term-form-component"; }

	get original() { return this.getAttribute("original"); }
	set original(value) { 
		this.setAttribute("original", value); 
		this.querySelector("input[name='original']").value = value;
	}

	get definition() { return this.getAttribute("definition"); }
	set definition(value) { 
		this.setAttribute("definition", value); 
		this.querySelector("input[name='definition']").value = value;
	}
	
	constructor() {
		super();
		this.classList.add("bg-base-100", "p-2", "flex", "m-3", "rounded-box", "disabled", "flex", "items-center");
		this.innerHTML = `
			<div class="relative">
				<input type="text" name="original" value="${this.original}" class="block rounded-t-base px-2.5 pb-2.5 pt-5 w-full text-sm text-heading bg-neutral-secondary-medium border-0 border-b-2 border-default-medium appearance-none focus:outline-none focus:ring-0 focus:border-brand peer" placeholder=" " />
				<label for="original" class="absolute text-sm text-body duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-fg-brand peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
					Original
				</label>
			</div>
			<div class="inline-block h-10 w-0.5 self-stretch bg-primary/30 mx-2"></div>
			<div class="relative">
				<input type="text" name="definition" value="${this.definition}" class="block rounded-t-base px-2.5 pb-2.5 pt-5 w-full text-sm text-heading bg-neutral-secondary-medium border-0 border-b-2 border-default-medium appearance-none focus:outline-none focus:ring-0 focus:border-brand peer" placeholder=" " />
				<label for="definition" class="absolute text-sm text-body duration-300 transform -translate-y-4 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-fg-brand peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-4 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto">
					Definition
				</label>
			</div>
			<div class="actions flex ml-3 flex-1">
				<input class="default btn w-fit btn-info" type="submit" name="action" value="Update">
				<div class="default inline-block h-10 w-0.5 self-stretch bg-primary/30 mx-2"></div>
				<input class="default btn w-fit btn-error" type="submit" name="action" value="Delete">
				<input class="create btn m-auto w-fit btn-success hidden" type="submit" name="action" value="Create">
			</div>
		`;
		this.querySelectorAll('input[type="submit"]').forEach(btn => {
			btn.addEventListener('click', e => {
				this.clickedAction = e.target.value;
			});
		});

		this.addEventListener("submit", async e => {
			e.preventDefault();
			let result;
			switch(this.clickedAction) {
				case "Update":
					result = await APIClient.updateTerm(this.id, this);
					break;
				case "Delete":
					result = await APIClient.deleteTerm(this.id);
					break;
				case "Create":
					result = await APIClient.addTerms(this.id, {terms:[this]})
					break;
			}
			if (result instanceof APIError) {
				result.errors.forEach(e => {
					const toast = Toast.create(e, "error", 3000);
					app.notifications.append(toast);
				})
			}
			else {
				if (this.clickedAction === "Delete") {
					this.remove();
					app.pageManager.reload();
				}
				const toast = Toast.create(result.message, "success", 1000);
				app.notifications.append(toast);
			}
		});
	}

	showCreate() {
		this.querySelectorAll(".default").forEach(e => e.classList.toggle("hidden", true));
		this.querySelectorAll(".create").forEach(e => e.classList.toggle("hidden", false));
	}

	showDefault() {
		this.querySelectorAll(".default").forEach(e => e.classList.toggle("hidden", false));
		this.querySelectorAll(".create").forEach(e => e.classList.toggle("hidden", true));
	}
	
	/**
	 * Description placeholder
	 *
	 * @static
	 * @param {number} id 
	 * @param {string} original 
	 * @param {string} definition 
	 * @returns {TermForm} 
	 */
	static create(id, original, definition) {
		const form = document.createElement("form", {is: this.name});
		form.original = original;
		form.definition = definition;
		form.id = id;
		return form;
	}
}

customElements.define(TermForm.name, TermForm, {extends: "form"});