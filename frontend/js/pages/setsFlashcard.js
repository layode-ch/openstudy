import { Card, Carousel, Flashcard } from "../components/index.js";
import  APIClient, { APIError } from "../modules/apiClient.js";
import Page from "../modules/page.js";
import app from "../script.js";
export default class SetsFlashcard extends Page {
	#set;
	#user;
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
		btnNext.addEventListener("click", () => this.#carousel.next())
		btnPrev.addEventListener("click", () => this.#carousel.previous())
		if (result) {
			title.textContent = this.#set.name;
			this.#terms.forEach(term => {
				const flashcard = Flashcard.create(term.original, term.definition);
				flashcard.classList.add("m-auto")
				this.#carousel.add(flashcard);
			});
		}
	}

	async #fetchData(setId) {
		this.#set = await APIClient.getSetById(setId);
		if (this.#set instanceof APIError) {
			this.displayErrors(set.errors);
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