/**
 * BLOCK: jrb-tabbed-content
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 */

//  Import CSS.
import './editor.scss';
import './style.scss';

const { __ } = wp.i18n; // Import __() from wp.i18n
const { registerBlockType } = wp.blocks; // Import registerBlockType() from wp.blocks
const { compose } = wp.compose;
const { withSelect } = wp.data;
import { InnerBlocks, useBlockProps, AlignmentToolbar, BlockControls } from '@wordpress/block-editor';

const ALLOWED_BLOCKS = [ 'jrb/block-jrb-tabbed-content--tab' ];

const setAnchor = (anchorString) => {
	if(!anchorString) {
		anchorString = `tab`;
	}
	let newAnchorNoSpecial = anchorString.replace(/&|\s*/g, '');
	newAnchorNoSpecial = newAnchorNoSpecial.replace(/[^a-zA-Z0-9]/, '').toLowerCase();
	return newAnchorNoSpecial;
}

const PrintTabs = (props) => {
	const tabs = props.tabs;
	return(tabs.map(tab => 
		<button 
		className="block-tabbed-content__tab" 
		data-default={tab[3]} 
		data-anchor={setAnchor(tab[1])+'-tab'} 
		data-interface="tab-button" 
		data-tab={tab[0]}><img width="30px" height="30px" alt="" src={tab[2]}/>{tab[1]}</button>));
}

const ChildTabs = (props) => {
	const { innerBlocks, className, attributes, setAttributes } = props;
		const tabsArray = innerBlocks.map( tab => [tab.clientId,tab.attributes.title,tab.attributes.iconid,tab.attributes.defaultTab,tab.attributes.tabAnchor] );
		const serialTabs = JSON.stringify(tabsArray);
		if( attributes.tabs !== serialTabs ) {
			setAttributes({tabs:serialTabs});
		}
		return <PrintTabs tabs={tabsArray}/>
}

const TabSelect = withSelect( ( select, blockData ) => {
	return {
		innerBlocks: select( 'core/block-editor' ).getBlocks( blockData.clientId )
	};
});

const GetChildTabs = compose(
	TabSelect
)(ChildTabs);

/**
 * Register: aa Gutenberg Block.
 *
 * Registers a new block provided a unique name and an object defining its
 * behavior. Once registered, the block is made editor as an option to any
 * editor interface where blocks are implemented.
 *
 * @link https://wordpress.org/gutenberg/handbook/block-api/
 * @param  {string}   name     Block name.
 * @param  {Object}   settings Block settings.
 * @return {?WPBlock}          The block, if it has been successfully
 *                             registered; otherwise `undefined`.
 */
registerBlockType( 'jrb/block-tabbed-content', {
	// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
	title: __( 'Tabbed Content' ), // Block title.
	description: __( 'This block allows for a tabbed content area to be setup' ),
	icon: 'table-row-after', // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
	category: 'design', // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
	keywords: [
		__( 'Tabbed Content' ),
		__( 'jrb' ),
		__( 'Tabs' ),
	],
	attributes: {
		tabs: {
			type: 'string',
			default: null
		},
		tabAlignment: {
			type: 'string',
			default: 'left'
		}
	},

	/**
	 * The edit function describes the structure of your block in the context of the editor.
	 * This represents what the editor will render when the block is used.
	 *
	 * The "edit" property must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Component.
	 */
	edit: ( props ) => {
		const {
			attributes: { tabAlignment },
			isSelected, className, setAttributes
		} = props;

		return( <div className={className}>
			{ isSelected && (
				<BlockControls>
					<AlignmentToolbar
						value={ tabAlignment }
						onChange={ tabAlignment => setAttributes( {tabAlignment } ) }
					/>
				</BlockControls>
			) }
					<div className={"block-tabbed-content__tabs block-tabbed-content__tabs--" + tabAlignment }>
						<GetChildTabs {...props}/>
					</div>
					<div className="block-tabbed-content__tab-panels">
						<InnerBlocks allowedBlocks={ALLOWED_BLOCKS}/>
					</div>
				</div> )
	},

	/**
	 * The sve function defines the way in which the different attributes should be combined
	 * into the final markup, which is then serialized by Gutenberg into post_content.
	 *
	 * The "save" property must be specified and must be a valid function.
	 *
	 * @link https://wordpress.org/gutenberg/handbook/block-api/block-edit-save/
	 *
	 * @param {Object} props Props.
	 * @returns {Mixed} JSX Frontend HTML.
	 */
	save: ( props) => {
		const blockProps = useBlockProps.save();
		const {attributes: {tabAlignment} } = props;
		const unpackedTabs = JSON.parse(props.attributes.tabs);

		//Check for defaults
		let has_defaults = 'false';
		unpackedTabs.forEach((tab) => {
			if(tab[3]) {
				has_defaults = 'true';
			}
		});
		const SaveTabs = () => (
			<div className={"block-tabbed-content__tabs init block-tabbed-content__tabs--" + tabAlignment} data-has-defaults={has_defaults}>
				<PrintTabs tabs={unpackedTabs}/>
			</div>
		);

		return (
			<div { ...blockProps }>
				<SaveTabs />
				<div className="block-tabbed-content__tab-panels init" data-has-defaults={has_defaults}>
					<InnerBlocks.Content />
				</div>
			</div>
		);
	},
	deprecated : [
		{
			attributes: {
				tabs: {
					type: 'string',
					default: null
				},
				tabAlignment: {
					type: 'string',
					default: 'left'
				}
			},
			save: (props) => {
				const blockProps = useBlockProps.save();
				const {attributes: {tabAlignment}} = props;
				const unpackedTabs = JSON.parse(props.attributes.tabs);
				const SaveTabs = () => (
					<div className={"block-tabbed-content__tabs init block-tabbed-content__tabs--" + tabAlignment}>
						<PrintTabs tabs={unpackedTabs}/>
					</div>
				);

				return (
					<div {...blockProps}>
						<SaveTabs/>
						<div className="block-tabbed-content__tab-panels init">
							<InnerBlocks.Content/>
						</div>
					</div>
				);
			}
		}
	]
} );
