import * as bootstrap from "bootstrap";
export default class Alert extends bootstrap.Alert {

	get fade() { return this.wrapper.classList.contains("fade"); }
	set fade(value) { this.wrapper.classList.toggle("fade", value); }

	get dismissible() { return this.wrapper.classList.contains("dismissible"); }
	set dismissible(value) { 
		if (!value)
			this.btnClose.style.display = "none";
		else 
			this.btnClose.style.display = "block";
		this.wrapper.classList.toggle("dismissible", value); 
	}

	constructor(message, dismissible = true, fade = true) {
		const wrapper = document.createElement("div");
		wrapper.classList.add("alert", "alert-warning", "alert-dismissible", "show", "fade");
		wrapper.innerHTML = `
			<h4 class="alert-heading" style="display: none"></h4>
			${message}
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		`;
		super(wrapper);
		this.wrapper = wrapper;
		this.btnClose = wrapper.querySelector(".btn-close");
		this.heading = wrapper.querySelector(".alert-heading");
		this.dismissible = dismissible;
		this.fade = fade;
	}
}