import * as boostrap from "bootstrap";
export default class Modal extends boostrap.Modal {
	constructor(title, text) {
		const wrapper = document.createElement("div");
		wrapper.classList.add("modal-dialog");
		wrapper.innerHTML = `
			<div class="">
				<div class="modal-content">
				<div class="modal-header">
					<h1 class="modal-title fs-5" id="exampleModalLabel">${title}</h1>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					${text}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
					<button type="button" class="btn btn-primary">Save changes</button>
				</div>
			</div>
		`;
		super(wrapper);
		this.wrapper = wrapper;
	}
}