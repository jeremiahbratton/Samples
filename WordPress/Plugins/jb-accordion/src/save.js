/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

import { InnerBlocks, RichText } from "@wordpress/block-editor";

/**
 * The save function defines the way in which the different attributes should
 * be combined into the final markup, which is then serialized by the block
 * editor into `post_content`.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#save
 *
 * @return {WPElement} Element to render.
 */
export default function save({ attributes }) {
	const { accordionTitle, accordionID, tagName } = attributes;

	return (
		<div className="jb-accordion" id={`jb-accordion-${accordionID}`}>
			<RichText.Content className="jb-accordion__header" tagName={tagName} value={accordionTitle} />
			<button
				aria-expanded="false"
				aria-controls={`accordion-panel-${accordionID}`}
				id={`accordion-header-${accordionID}`}
				className="jb-accordion__toggle has-background transparent has-text-color"
			>
				<RichText.Content tagName={tagName} value={accordionTitle} />
				<svg
					aria-hidden="true"
					focusable="false"
					className="svg jb-accordion__icon"
					role="img"
					xmlns="http://www.w3.org/2000/svg"
					viewBox="0 0 448 512"
				>
					<path
						fill="currentColor"
						d="M207.029 381.476L12.686 187.132c-9.373-9.373-9.373-24.569 0-33.941l22.667-22.667c9.357-9.357 24.522-9.375 33.901-.04L224 284.505l154.745-154.021c9.379-9.335 24.544-9.317 33.901.04l22.667 22.667c9.373 9.373 9.373 24.569 0 33.941L240.971 381.476c-9.373 9.372-24.569 9.372-33.942 0z"
					></path>
				</svg>
			</button>
			<div
				id={`accordion-panel-${accordionID}`}
				className="jb-accordion__content"
				aria-hidden="true"
				role="region"
				aria-labelledby={`accordion-header-${accordionID}`}
			>
				<InnerBlocks.Content />
			</div>
		</div>
	);
}
