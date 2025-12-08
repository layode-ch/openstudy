import APIClient from "../modules/apiClient.js";
import Page from "../modules/page.js";

export default class SetsEdit extends Page {
	async init() {
		const urlParams = new URLSearchParams(window.location.search);
		const id = urlParams.get("id");
		const set = await APIClient.getSetById(id);
		const user = await APIClient.getUserById(set.user_id);
		console.log(set);
		console.log(user);
	}
}