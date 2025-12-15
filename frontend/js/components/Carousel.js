export class Carousel extends HTMLDivElement {

	static get name() { return "carousel-component"; }

	/** @type {SlidesManager} */
	#slides

	/** @readonly */
	get currentIndex() { return this.#slides.currentIndex; }

	constructor() {
		super();

		if (this.id === "") {
			this.id = this.#generateId();
		}
		this.#slides = new SlidesManager(this.id);
		this.classList.add("carousel")
		const body = document.createElement("div");
		body.innerHTML = this.innerHTML;
		this.innerHTML = "";
		Array.from(body.children).forEach(e => {
			this.add(e);
		});
		

	}

	#generateId() {
		return "id-" + Math.random().toString(16).slice(2);
	}

	/**
	 * Add slides to this carousel
	 * @param {...HTMLElement} elements
	 */
	add(...elements) {
		elements.forEach(e => {
			const item = document.createElement("div");
			item.classList.add("carousel-item", "relative", "w-full");
			item.append(e);
			this.append(item);
			this.#slides.push(item)
		})
	}


	removeChild(child) {
		this.#slides.remove(child);
		return super.removeChild(child);
	}

	

	next() {
		if (this.#slides.length === 0) return;

		// Move index
		this.#slides.currentIndex++;
		if (this.#slides.currentIndex >= this.#slides.length) {
			this.#slides.currentIndex = 0; // wrap
		}

		// Get the next slide element
		const slide = this.#slides[this.#slides.currentIndex];

		// Same effect as <a href="#id">
		location.hash = "#" + slide.id;
	}

	previous() {
		if (this.#slides.length === 0) return;

		// go back
		this.#slides.currentIndex--;
		if (this.#slides.currentIndex < 0) {
			this.#slides.currentIndex = this.#slides.length - 1; // wrap
		}

		const slide = this.#slides[this.#slides.currentIndex];
		location.hash = "#" + slide.id;
	}

}

/**
 * Manages slides and assigns unique sequential IDs.
 *
 * @class SlidesManager
 * @extends {Array<HTMLElement>}
 */
class SlidesManager extends Array {

	/**
	 * @param {string} carouselId
	 */
	constructor(carouselId) {
		super();
		this.carouselId = carouselId;
		this.currentIndex = 0;
	}

	push(...items) {
		items.forEach((el, index) => {
			const newId = `${this.carouselId}-${this.length + index + 1}`;
			el.id = newId;
			super.push(el);
		});

		return this.length;
	}

	remove(...items) {
		for(const item of items) {
			const index = this.indexOf(item);
			if (index >= 0) 
				this.splice(index, 1);
		}
	}
}

customElements.define(Carousel.name, Carousel, { extends: "div" });