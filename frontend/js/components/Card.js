import { animate, waapi, eases, spring, JSAnimation } from 'animejs';

export class Card extends HTMLDivElement {
	static get name() { return "card-component"; }

	
    get fade() { 
        const value = this.getAttribute("fade");
        return value !== null && value !== "false" 
    }
    set fade (value) { 
		this.setAttribute("fade", value.toString());
		this.#setAnimation();
	}
	
	get delay() { return Number(this.getAttribute("delay")); }
	set delay(value) { return this.setAttribute("delay", value); }
	
	get src() { return this.getAttribute("src"); }
	set src(value) {
		this.#figure.classList.toggle("hidden", (value == null));
		return this.setAttribute("src", value); 
	}

	
	/**
	 * Test
	 *
	 * @type {string}
	 */
	get title() { return this.getAttribute("title"); }
	set title(value) { 
		this.setAttribute("title", value);
		this.querySelector(".card-title").textContent = value;
	}

	#figure;
	#title;
	/**
	 * Description placeholder
	 *
	 * @readonly
	 * @type {HTMLDivElement}
	 */
	get body() { return this.#body;}
	#body;
	constructor() {
		super();
		this.classList.add("card", "m-5");
		const html = this.innerHTML;
		this.innerHTML = `
			<figure>	
				<img
				src="${this.src}"
				alt="Shoes" />
			</figure>
			<div class="card-body">
				<h2 class="card-title">${this.title}</h2>
				${html}
  			</div>
		`;
		this.#body = this.querySelector(".card-body");
		this.#figure = this.querySelector("figure");
		this.#figure.classList.toggle("hidden", (this.src == null));
		this.#setAnimation();
		this.addEventListener("mouseover", () => {
			waapi.animate(this, {
				y: {
					to: "-1rem"
				},
				ease: spring({ stiffness: 100 }),
			});
		});
		this.addEventListener("mouseleave", () => {
			waapi.animate(this, {
				y: {
					to: "0"
				},
				ease: spring({ stiffness: 100 }),
			});
		});
	}

	connectedCallback() {
		setTimeout(() => {this.animation.play()}, this.delay);
	}

	#setAnimation() {
		const opacity = this.fade ? 0 : 1;
		this.animation = waapi.animate(this, {
			y: {
				from: "3rem",
				to: "0"
			},
			opacity: {
				from: opacity,
				to: 1,
				duration: 200
			},
			ease: spring({ stiffness: 100 }),
			autoplay: false
		});
	}

	close() {
		this.animation.onComplete = () => {
			this.remove();
		}
		this.animation.reverse();
	}

	/**
	 * Creates an instance of `Card`
	 *
	 * @static
	 * @returns {Card}
	 */
	static create() {
		/** @type {Card} */
		// @ts-ignore
		const card = document.createElement("div", { is: Card.name});
		return card;
	}

	attributeChangedCallback(name, oldValue, newValue) {
		console.log("hhe")
		if (name === "title" && this.#title) {
			this.#title.textContent = newValue;
		}
	}
}
customElements.define(Card.name, Card, { extends: "div" });