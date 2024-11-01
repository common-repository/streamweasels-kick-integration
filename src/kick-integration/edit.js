import {
	PanelBody,
	PanelRow,
	TextControl,
	QueryControls,
	ToggleControl,
	SelectControl,
	RangeControl
} from '@wordpress/components';

import { useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';


/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
import { __ } from '@wordpress/i18n';

/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @return {WPElement} Element to render.
 */
export default function Edit({ attributes, setAttributes }) {

	const { 
		layout,
		channels,
		limit,
	} = attributes;

	const [data, setData] = wp.element.useState([]);
	const [currentSlide, setCurrentSlide] = useState(0);

	let layoutArray;

	// Set channelsArray to channels first
	let channelsArray = channels ? channels.split(',') : [];

	wp.apiFetch({ path: '/streamweasels-kick/v1/data' }).then(response => {
		setData(response);
	});

	const handlePrevious = () => {
        setCurrentSlide((prevSlide) => (prevSlide - 1 + channelsArray.length) % channelsArray.length);
    };

    const handleNext = () => {
        setCurrentSlide((prevSlide) => (prevSlide + 1) % channelsArray.length);
    };

	if (data.proStatus) {
		layoutArray = [
			{label: 'Wall', value: 'wall'},
			{label: 'Status', value: 'status'},
			{label: 'Vods', value: 'vods'},
			{label: 'Feature', value: 'feature'}
		];
	} else {
		layoutArray = [
			{label: 'Wall', value: 'wall'},
			{label: 'Status', value: 'status'},
			{label: 'Vods', value: 'vods'},
		];
	}	

	return (
		<>
		<InspectorControls>		
			<PanelBody title={ __( 'Kick Integration Settings', 'streamweasels-kick-integration' ) }>
				<PanelRow>
					<SelectControl
						label={ __(
							'Layout',
							'streamweasels'
						) }
						help={ __(
							'Choose the desired layout for your streams.',
							'streamweasels'
						) }						
						value={ layout }
						onChange={ ( layout ) =>
							setAttributes( { layout: layout } )
						}
						options={ layoutArray }
					/>
				</PanelRow>					
				<PanelRow>
					<TextControl
						label={ __(
							'Kick Channels',
							'streamweasels-kick-integration'
						) }
						help={ __(
							'Enter the Kick channels you want to display, separated by commas.',
							'streamweasels-kick-integration'
						) }
						value={ channels }
						onChange={ ( channels ) => setAttributes( { channels: channels} ) }
					/>
				</PanelRow>
				<PanelRow>
					<div>
						<RangeControl
							label="Number of Streams"
							help={ __(
								'Enter the number of streams to display.',
								'streamweasels'
							) }							
							value={ limit }
							onChange={ ( value ) => setAttributes( { limit: value } ) }
							min={ 1 }
							max={ 50 }
						/>
					</div>
				</PanelRow>
			</PanelBody>
			<PanelBody title={ __( 'Kick Advanced Settings', 'streamweasels' ) }>
				<PanelRow>
					<div>
						<p>Looking to customise your Kick Integration even further? Check out the <a href="admin.php?page=streamweasels-kick" target="_blank">Kick Integration Settings</a> page for more options.</p>
					</div>
				</PanelRow>
			</PanelBody>				
		</InspectorControls>
		<div { ...useBlockProps() }>
			<div class="cp-swki" data-columns="4" data-colour="light" data-layout={layout}>

				{layout === 'status' && (
					<>
						<div className="cp-swki__twitch-logo">
							<span></span>
						</div>
						<div className="cp-swki__player-list">
							<div className="cp-swki__stream">
								<p><strong>{channelsArray[0]}</strong></p>
								<p>Streaming X for 100 Viewers.</p>
							</div>
						</div>
					</>
				)}

				{layout === 'feature' && (
					<>
						<button className="cp-swki__arrow cp-swki__arrow-left" onClick={handlePrevious}>←</button>
						<button className="cp-swki__arrow cp-swki__arrow-right" onClick={handleNext}>→</button>
						<div className="cp-swki__container" style={{ transform: `translateX(-${(currentSlide * 33.33)}%)` }}>
							{channelsArray.slice(0, limit).map((channel, index) => (
								<div key={index} className={`cp-swki__stream ${index === currentSlide + 1 ? 'active' : ''}`}>
									{channel}
								</div>
							))}
						</div>
					</>
				)}	

				{layout === 'wall' && (
					<>
						{channelsArray.slice(0, limit).map((channel, index) => (
							<div key={index} className="cp-swki__stream">
								{channel}
							</div>
						))}
					</>	
				)}

				{layout === 'vods' && (
					<>
						{Array.from({ length: limit }, (_, i) => (
							<div key={i} className="cp-swki__stream">
								CLIP
							</div>
						))}
					</>	
				)}				
			</div>
		</div>
		</>
	);
}
