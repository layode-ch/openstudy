import { Card } from "./Card.js";

export class Flashcard extends HTMLLabelElement {

	static get name() { return "flashcard-component"; }

	get original() { return this.getAttribute("original"); }
	set original(value) { 
		this.setAttribute("original", value); 
		this.querySelector(".original").textContent = value;
	}

	get definition() { return this.getAttribute("definition"); }
	set definition(value) { 
		this.setAttribute("definition", value); 
		this.querySelector(".definition").textContent = value;
	}


	constructor() {
		super();
		this.classList.add("swap", "swap-flip");
		this.innerHTML = `
			<input type="checkbox" />
			<div class="swap-off flex item-center">
				<div is="${Card.name}" fade class="bg-base-100 w-96 shadow-xl">
					<span class="m-auto align-middle original text-3xl">${this.original}</span>
				</div>
			</div>
			<div class="swap-on flex item-center">
				<div is="${Card.name}" fade class="bg-base-100 w-96 shadow-xl">
					<span class="m-auto align-middle h-fit definition text-3xl">${this.definition}</span>
				</div>
			</div>
		`;

	}

	static create(original, definition) {
		/** @type {Flashcard} */
		const flashcard = document.createElement("label", { is:this.name });
		flashcard.original = original;
		flashcard.definition = definition;
		return flashcard;
	}
}

customElements.define(Flashcard.name, Flashcard, { extends: "label" });