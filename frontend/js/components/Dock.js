export default class Dock extends HTMLDivElement {
	
	/**
	 * Name of the custom `Dock` component
	 *
	 * @static
	 * @readonly
	 * @type {string}
	 */
	static get name() { return "dock-component"; }

	/**
	 * Description placeholder
	 *
	 * @readonly
	 * @type {NodeListOf<HTMLButtonElement>}
	 */
	get entries() { return this.querySelectorAll("button.dock-entry"); }

	constructor() {
		super();
		this.classList.add("dock");
	}

	static create() {
		const dock = document.createElement("div", {is: Dock.name});
		return dock;
	}
}

customElements.define(Dock.name, Dock, { extends: "div" });