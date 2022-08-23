var EditArea_keybinding = {
	/**
	 * Is called each time the user touch a keyboard key.
	 *	 
	 * @param (event) e: the keydown event
	 * @return true - pass to next handler in chain, false - stop chain execution
	 * @type boolean	 
	 */
	onkeydown: function(e) {
		console.log('keybinding plugin');
		if (window.top && window.top.keybindingHandler) {
			return window.top.keybindingHandler(e);
		}

		return true;
	}
};

editArea.add_plugin("keybinding", EditArea_keybinding);
