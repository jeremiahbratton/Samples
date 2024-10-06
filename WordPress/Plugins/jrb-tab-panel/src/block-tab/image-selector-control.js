// Load dependencies
const { __ } = wp.i18n;
const { Component } = wp.element;
const { MediaUpload, MediaUploadCheck } = wp.editor;
const { PanelBody, Button, ResponsiveWrapper, Spinner } = wp.components;
const { compose } = wp.compose;
const { withSelect } = wp.data;

const ALLOWED_MEDIA_TYPES = [ 'image' ];

class ImageSelectorEdit extends Component {
	render() {
		const { attributes, setAttributes, bgImage, className } = this.props;
		const { iconid } = attributes;
		const instructions = <p>{ __( 'To edit the background image, you need permission to upload media.', 'image-selector-example' ) }</p>;

		let styles = {};
		if ( bgImage && bgImage.source_url ) {
			styles = { backgroundImage: `url(${ bgImage.source_url })` };
		}

		const onUpdateImage = ( image ) => {
			setAttributes( {
				iconid: image.id,
			} );
		};

		const onRemoveImage = () => {
			setAttributes( {
				iconid: undefined,
			} );
		};

		return (
						<div className="wp-block-image-selector-example-image">
							<MediaUploadCheck fallback={ instructions }>
								<MediaUpload
									title={ __( 'Background image', 'image-selector-example' ) }
									onSelect={ onUpdateImage }
									allowedTypes={ ALLOWED_MEDIA_TYPES }
									value={ iconid }
									render={ ( { open } ) => (
										<Button
											className={ ! iconid ? 'editor-post-featured-image__toggle' : 'editor-post-featured-image__preview' }
											onClick={ open }>
											{ ! iconid && ( __( 'Set background image', 'image-selector-example' ) ) }
											{ !! iconid && ! bgImage && <Spinner /> }
											{ !! iconid && bgImage &&
											<ResponsiveWrapper
												naturalWidth={ bgImage.media_details.width }
												naturalHeight={ bgImage.media_details.height }
											>
												<img src={ bgImage.source_url } alt={ __( 'Background image', 'image-selector-example' ) } />
											</ResponsiveWrapper>
											}
										</Button>
									) }
								/>
							</MediaUploadCheck>
							{ !! iconid && bgImage &&
							<MediaUploadCheck>
								<MediaUpload
									title={ __( 'Background image', 'image-selector-example' ) }
									onSelect={ onUpdateImage }
									allowedTypes={ ALLOWED_MEDIA_TYPES }
									value={ iconid }
									render={ ( { open } ) => (
										<Button onClick={ open } isDefault isLarge>
											{ __( 'Replace background image', 'image-selector-example' ) }
										</Button>
									) }
								/>
							</MediaUploadCheck>
							}
							{ !! iconid &&
							<MediaUploadCheck>
								<Button onClick={ onRemoveImage } isLink isDestructive>
									{ __( 'Remove background image', 'image-selector-example' ) }
								</Button>
							</MediaUploadCheck>
							}
						</div>
		);
	}
}

export default compose(
	withSelect( ( select, props ) => {
		const { getMedia } = select( 'core' );
		const { iconid } = props.attributes;

		return {
			bgImage: iconid ? getMedia( iconid ) : null,
		};
	} ),
)( ImageSelectorEdit );
