/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-i18n/
 */
import { __ } from "@wordpress/i18n";

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/packages/packages-block-editor/#useBlockProps
 */
import {
	useBlockProps,
	RichText,
	InnerBlocks,
	BlockControls,
} from "@wordpress/block-editor";

/**
 * @see https://reactjs.org/docs/hooks-reference.html#useeffect
 */

import { useEffect } from "@wordpress/element";

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import "./editor.scss";

import HeadingLevelDropdown from "./header/header-level-dropdown";

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/developers/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {
	//add id to accordion
	const blockProps = useBlockProps();

	const { accordionTitle, accordionID, titleLevel, tagName } = attributes;

	useEffect(() => {
		setAttributes({ accordionID: blockProps.id });
	}, []);

	//needed for async atrributes to work
	useEffect(() => {
		setAttributes({ tagName: `h${titleLevel}` });
	}, [titleLevel, tagName]);

	//exclude self form accordion content
	const excludeSelf = wp.blocks
		.getBlockTypes()
		.map((block) => block.name)
		.filter((name) => name !== "jb/accordion");

	return (
		<>
			<BlockControls group="block">
				<HeadingLevelDropdown
					selectedLevel={titleLevel}
					onChange={(newLevel) => {
						setAttributes({ titleLevel: newLevel, tagName: "h" + titleLevel });
					}}
				/>
			</BlockControls>
			<div
				{...useBlockProps({
					className: "jb-accordion",
				})}
			>
				<RichText
					tagName={tagName}
					placeholder={__("Accordion Title", "jb-accordion")}
					value={accordionTitle}
					className="jb-accordion__title"
					onChange={(val) => {
						setAttributes({ accordionTitle: val });
					}}
					allowedFormats={["core/bold", "core/italic"]}
				/>
				<div class="jb-accordion__content">
					<InnerBlocks
						allowedBlocks={excludeSelf}
						template={[
							[
								"core/paragraph",
								{
									placeholder: __(
										"Add Accordion Content…",
										"jb-accordion"
									),
								},
							],
						]}
					/>
				</div>
			</div>
		</>
	);
}
