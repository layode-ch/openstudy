import { Card, Carousel, Flashcard, TermForm, Toast } from "../components/index.js";
import  APIClient, { APIError } from "../modules/apiClient.js";
import Page from "../modules/page.js";
import app from "../script.js";
export default class SetsFlashcard extends Page {
	#set;
	#user;
	/** @type {any[]} */
	#terms;
	/** @type {Carousel} */
	#carousel;
	async init() {
		const urlParams = new URLSearchParams(window.location.search);
		const id = urlParams.get("id");
		const result = await this.#fetchData(id);
		this.#carousel = this.main.querySelector("#card-carousel");
		const btnPrev = this.main.querySelector("#prev");
		const btnNext = this.main.querySelector("#next");
		const title = this.main.querySelector("#set-title");
		const terms = this.main.querySelector("#terms")
		btnNext.addEventListener("click", () => {
			this.#carousel.next(); 
			window.scrollTo(0, 0);
		})
		btnPrev.addEventListener("click", () => {
			this.#carousel.previous(); 
			window.scrollTo(0, 0);
		})
		if (result) {
			title.textContent = this.#set.name;
			this.#createCardsAndSets(terms);
			if (this.#set.user_id === APIClient.userId) {
				const form = TermForm.create(this.#set.id, "", "");
				form.addEventListener("submit", () => {this.#termFormOnsubit(form, null)})
				terms.append(form);
				form.showCreate();
			}
		}
	}

	#createCardsAndSets(terms) {
		this.#terms.forEach(term => {
			let flashcard = Flashcard.create(term.original, term.definition);
			flashcard.classList.add("m-auto")
			this.#carousel.add(flashcard);
			const form = TermForm.create(term.id, term.original, term.definition);
			form.addEventListener("submit", e => {
				this.#termFormOnsubit(form, flashcard);
			});
			terms.append(form);
			if (APIClient.userId !== this.#set.user_id)
				form.hideActions();
		});
	}
	#termFormOnsubit(form, flashcard) {
		const data = new FormData(form);
		switch(form.clickedAction) {
			case "Update":
				flashcard.original = data.get("original");
				flashcard.definition = data.get("definition");
				break
			case "Create":
				flashcard = Flashcard.create(data.get("original"), data.get("definition"));
				form.showDefault();
				this.#carousel.add(flashcard);
				const newForm = TermForm.create(this.#set.id, "", "");
				newForm.showCreate();
				newForm.addEventListener("submit", e => {
					this.#termFormOnsubit(newForm, null);
				});
				terms.append(newForm);
				break;
		}
	}
	async #fetchData(setId) {
		this.#set = await APIClient.getSetById(setId);
		if (this.#set instanceof APIError) {
			this.displayErrors(this.#set.errors);
			return false;
		}
		this.#user = await APIClient.getUserById(this.#set.user_id);
		if (this.#user instanceof APIError) {
			this.displayErrors(this.#set.errors);
			return false;
		}
		this.#terms = await APIClient.getTermsBySetId(setId);
		if (this.#terms instanceof APIError) {
			this.displayErrors(this.#set.errors);
			return false;
		}

		return true;
	} 
}