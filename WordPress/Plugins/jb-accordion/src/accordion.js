const Accordion = () => {
	//grab all accordion triggers
	const accordionToggles = document.querySelectorAll(
		".jb-accordion__toggle"
	);

	//grab all accordion content
	const accordionContents = document.querySelectorAll(
		".jb-accordion__content"
	);

	//mobile check
	const isMobile = window.matchMedia("(max-width: 768px)");

	const touchSupported = "ontouchstart" in window;
	const pointerSupported = "pointerdown" in window;

	let timeout;

	const skipClickDelay = (e) => {
		e.preventDefault();
		e.target.click();
	};

	//Sets attributes on each element
	const setAriaAttr = (el, ariaType, newProperty) => {
		el.setAttribute(ariaType, newProperty);
	};

	//Set the accesibility for the accordion.
	const setAccordionAria = (el1, el2, expanded) => {
		switch (expanded) {
			case "true":
				setAriaAttr(el1, "aria-expanded", "true");
				setAriaAttr(el2, "aria-hidden", "false");
				break;
			case "false":
				setAriaAttr(el1, "aria-expanded", "false");
				setAriaAttr(el2, "aria-hidden", "true");
				break;
			default:
				break;
		}
	};

	//toggle the accordion properties
	const switchAccordion = (e) => {
		e.preventDefault();
		let thisTrigger = e.target;
		console.log("thisTrigger", thisTrigger);
		//if caputring event within button text buubble up to parent.
		if (!thisTrigger.classList.contains("jb-accordion__toggle")) {
			thisTrigger = thisTrigger.parentNode;
		}
		const thisContent = thisTrigger.nextElementSibling;

		if (thisContent.classList.contains("is-collapsed")) {
			setAccordionAria(thisTrigger, thisContent, "true");
		} else {
			setAccordionAria(thisTrigger, thisContent, "false");
		}
		// thisTrigger.classList.toggle("is-collapsed");
		thisTrigger.classList.toggle("is-expanded");
		thisContent.classList.toggle("is-collapsed");
		thisContent.classList.toggle("is-expanded");

		thisContent.classList.toggle("animateIn");
	};

	//check if mobile or desktop

	//if mobile then remove collpased and click events

	const initAccordion = () => {
		for (let i = 0, len = accordionToggles.length; i < len; i++) {
			if (touchSupported) {
				accordionToggles[i].addEventListener(
					"touchstart",
					skipClickDelay,
					false
				);
			}
			if (pointerSupported) {
				accordionToggles[i].addEventListener(
					"pointerdown",
					skipClickDelay,
					false
				);
			}
			//setAriaAttr(accordionToggles[i], "aria-hidden", "false");
			accordionToggles[i].addEventListener("click", switchAccordion, false);
		}

		for (let i = 0, len = accordionContents.length; i < len; i++) {
			//setAriaAttr(accordionContents[i], "aria-hidden", "true");
			accordionContents[i].classList.add("is-collapsed");
		}
	};

	//destroy and remove functionality from accordion
	const destroyAccordion = () => {
		for (let i = 0, len = accordionToggles.length; i < len; i++) {
			//setAriaAttr(accordionToggles[i], "aria-hidden", "true");
			accordionToggles[i].removeEventListener("click", switchAccordion, false);
		}

		for (let i = 0, len = accordionContents.length; i < len; i++) {
			//setAriaAttr(accordionContents[i], "aria-hidden", "false");
			accordionContents[i].classList.remove("is-collapsed");
		}
	};

	//toggle crate and destroy for browser events
	const toggleLifecycle = () => {
		if (!isMobile.matches) {
			destroyAccordion();
		} else {
			initAccordion();
		}
	};

	//size check on window resize
	window.onresize = () => {
		//debouncing to prevent multiple calls
		clearTimeout(timeout);
		timeout = setTimeout(toggleLifecycle, 250);
	};

	//init accordion
	window.onload = toggleLifecycle;
};

//Load the accordion
document.addEventListener("DOMContentLoaded", function () {
	Accordion();
});
