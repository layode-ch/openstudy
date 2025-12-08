import { Card } from "./Card.js";

export class SetCard extends Card {
	
	static get name() { return "set-card-component"; }

	get description() { 
		const value = this.getAttribute("description");
		return value == null ? "" : value;
	}
	set description(value) {
		this.setAttribute("description", value);
		this.querySelector(".set-description").textContent = value;
	}

	get author() {
		const value = this.getAttribute("author");
		return value == null ? "" : value;
	}
	set author(value) {
		this.setAttribute("author", value);
		this.querySelector(".set-author").textContent = value;
	}

	constructor() {
		super();
		this.fade = true;
		this.body.innerHTML += `
			<hr class="h-1 w-full bg-base-300 rounded-full my-1 border-0"> 
			<p class="set-description">${this.description}</p>
			<p class="set-author ml-auto">Made by: ${this.author}</p>
		`;
		this.classList.add("bg-base-100", "w-96", "shadow-xl");
	}

	/**
	 * Creates an instance of `SetCard`
	 *
	 * @static
	 * @param {string} name
	 * @param {string} description 
	 * @param {string} author 
	 * @returns {SetCard}
	 */
	static create(name, description, author) {
		const setCard = document.createElement("div", { is:SetCard.name});
		setCard.title = name;
		setCard.description = description;
		setCard.author = author;
		return setCard;
	}
}

customElements.define(SetCard.name, SetCard, { extends: "div" });