import { Alert, SetCard } from "../components/index.js";
import APIClient, { APIError } from "../modules/apiClient.js";
import Page from "../modules/page.js";

export default class Sets extends Page {
	/** @type {HTMLDivElement} */
	#userSets;
	/** @type {HTMLDivElement} */
	#sets;

	#userCache = new Map();
	async init() {
		this.#userSets = document.querySelector(".user-sets");
		this.#sets = document.querySelector(".sets");
		const sets = await APIClient.searchSets();
		for (let i = 0; i < sets.length; i++) {
			await this.#displaySet(sets[i], i);
		}
		this.#checkSets();
	}

	
	/**
	 * Description placeholder
	 *
	 * @param {{
	 * id: number,
	 * name: string,
	 * description: string,
	 * user_id: number
	 * }} set 
	 */
	async #displaySet(set, index) {
		let user = {username: ""};
		const a = document.createElement("a");
		if (this.#userCache.has(set.user_id))
			user.username = this.#userCache.get(set.user_id);
		else {
			user = await APIClient.getUserById(set.user_id);
			this.#userCache.set(user.id, user.username);
		}
		const setCard = SetCard.create(set.name, set.description, user.username);
		setCard.delay = index * 100;
		a.href = `/sets/edit?id=${set.id}`;
		a.append(setCard);
		if (set.user_id === APIClient.userId) {
			this.#userSets.append(a);
		}
		else {
			this.#sets.append(a);
		}
	}

	#checkSets() {
		if (this.#userSets.children.length == 0)
			this.#userSets.append(Alert.create("You didn't create any sets", "warning", true));
		if (this.#sets.children.length == 0)
			this.#sets.append(Alert.create("There's no sets made by other users", "warning", true));
	}
}