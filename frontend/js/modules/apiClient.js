export default class APIClient {
	
	static API_BASE = `${window.origin}/api`;

	static get token() { return localStorage.getItem("token"); }
	static set token(value) { localStorage.setItem("token", value); }

	static get userId() { return Number(localStorage.getItem("user_id")); }
	static set userId(value) { localStorage.setItem("user_id", value); }

	static getHeaders() {
		const headers = { "Content-Type": "application/json" };
		if (this.token) {
			headers["Authorization"] = `Bearer ${this.token}`;
		}
		return headers;
	}

	static async checkToken() { 
		const result = await this.request("GET", "/user/auth"); 
		if (!(result instanceof APIError))
			this.userId = result.id;
		return !(result instanceof APIError);
	}

	/**
	 * Effectue une requête générique à l'API.
	 * @param {string} method
	 * @param {string} endpoint
	 * @param {Object|null} data
	 * @param {boolean} includeAuth
	 * @returns {Promise<Object|APIError>}
	 */
	static async request(method, endpoint, data = null) {
		const url = `${this.API_BASE}${endpoint}`;
		const config = {
			method,
			headers: this.getHeaders()
		};

		if (data) {
			config.body = JSON.stringify(data);
		}

		try {
			const response = await fetch(url, config);
			const result = await response.json();

			// API returns an errors key -> wrap in APIError
			if (result && typeof result === "object" && result.errors) {
				return new APIError(result.errors);
			}

			if (!response.ok) {
				// If no structured errors, still wrap a generic error
				return new APIError([result.message || `HTTP ${response.status}`]);
			}

			return result;
		} catch (error) {
			console.error(`API Error [${method} ${endpoint}]:`, error);
			return new APIError([error.message || "Network error"]);
		}
	}

	static async login(data) {
		return await this.request("POST", "/user/login", data);
	}

	static async signUp(data) {
		return await this.request("POST", "/user/sign-up", data);
	}

	static async searchSets() {
		return await this.request("GET", "/set/search");
	}

	static async getUserById(id) {
		return await this.request("GET", `/user/${id}`);
	}

	static async getSetById(id) {
		return await this.request("GET", `/set/${id}`);
	}

	
	static async getTermsBySetId(id) {
		return await this.request("GET", `/set/${id}/terms`);
	}

}

export class APIError {
	/**
	 * @param {Array|string|Object} errors
	 */
	constructor(errors = []) {
		if (errors === null) errors = [];
		this.errors = Array.isArray(errors) ? errors : [errors];
	}

	/**
	 * Human readable message
	 * @returns {string}
	 */
	message() {
		return this.errors.map(e => (typeof e === "string" ? e : JSON.stringify(e))).join("\n");
	}
}