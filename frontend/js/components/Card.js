import { animate, waapi, eases, spring, JSAnimation } from 'animejs';

export class Card extends HTMLDivElement {
	static get name() { return "card-component"; }

	
    get fade() { 
        const value = this.getAttribute("fade");
        return value !== null && value !== "false" 
    }
    set fade (value) { this.setAttribute("fade", value.toString()); }
	
	get delay() { return Number(this.getAttribute("delay")); }
	set delay(value) { return this.setAttribute("delay", value); }
	
	get src() { return this.getAttribute("src"); }
	set src(value) { return this.setAttribute("src", value); }

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
		
		const opacity = this.fade ? 0 : 1;
		this.animation = waapi.animate(this, {
			y: {
				from: "3rem"
			},
			opacity: { 
				from: opacity,
				duration: 200
			},
			ease: spring({ stiffness: 100 }),
			autoplay: false
		});

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

	close() {
		this.animation.onComplete = () => {
			this.remove();
		}
		this.animation.reverse();
	}

	/**
	 * Creates an instance of `Card`
	 *
	 * @param {string} message 
	 * @static
	 * @returns {Card}
	 */
	static create(message) {
		/** @type {Card} */
		// @ts-ignore
		const card = document.createElement("div", { is: Card.name });
		card.message = message;
		return card;
	}
}
customElements.define(Card.name, Card, { extends: "div" });