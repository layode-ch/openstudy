import { SetCard } from "../components/index.js";
import APIClient, { APIError } from "../modules/apiClient.js";
import Page from "../modules/page.js";

export default class Sets extends Page {
	/** @type {HTMLDivElement} */
	#userSets;
	/** @type {HTMLDivElement} */
	#sets;
	async init() {
		this.#userSets = document.querySelector(".user-sets");
		this.#sets = document.querySelector(".sets");
		const sets = await APIClient.searchSets();
		sets.forEach(this.#displaySet.bind(this));
	}

	
	/**
	 * Description placeholder
	 *
	 * @param {{
	 * id: number,
	 * name: string,
	 * description: string,
	 * user_id: string
	 * }} set 
	 */
	#displaySet(set, index) {
		const setCard = SetCard.create(set.name, set.description, set.user_id);
		setCard.delay = index * 100;
		if (set.user_id === APIClient.userId) {
			console.log("appended");
			this.#userSets.append(setCard);
		}
		else {
			this.#sets.append(setCard);
		}
	}
}