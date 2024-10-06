/**
 * WordPress dependencies
 */
import { ToolbarDropdownMenu } from "@wordpress/components";
import { __, sprintf } from "@wordpress/i18n";

import HeadingLevelIcon from "./heading-level-icon";

const HEADING_LEVELS = [1, 2, 3, 4, 5, 6];

/**
 * Dropdown for selecting a heading level (1 through 6).
 *
 * @param {WPHeadingLevelDropdownProps} props Component props.
 *
 * @return {WPComponent} The toolbar.
 */
export default function HeadingLevelDropdown({ selectedLevel, onChange }) {
	return (
		<ToolbarDropdownMenu
			icon={<HeadingLevelIcon level={selectedLevel} />}
			label={__("Change heading level", "bbuilds-accordion")}
			controls={HEADING_LEVELS.map((targetLevel) => {
				{
					const isActive = targetLevel === selectedLevel;

					return {
						icon: <HeadingLevelIcon level={targetLevel} isPressed={isActive} />,
						label: sprintf(
							// translators: %s: heading level e.g: "1", "2", "3"
							__("Heading %d", "bbuilds-accordion"),
							targetLevel
						),
						title: sprintf(
							// translators: %s: heading level e.g: "1", "2", "3"
							__("Heading %d", "bbuilds-accordion"),
							targetLevel
						),
						isActive,
						onClick() {
							onChange(targetLevel);
						},
					};
				}
			})}
		/>
	);
}
