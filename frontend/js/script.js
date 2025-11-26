import Modal from "./components/modal.js";
import Alert from "./components/alert.js";
const alert = new Alert("Hiiiiiiiii", false);
const modal = new Modal("Title", "message")
document.body.append(alert.wrapper, modal);
modal.show()
console.log(alert.btnClose);