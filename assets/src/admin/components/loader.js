import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import '../styles/react-toastify.scss';

const Loader = () => {
	return (
		<div className="loader">
			<img
				src="https://res.cloudinary.com/d-coders/image/upload/v1592201998/wp-plugins/elitbuzzsms.gif"
				alt="elitbuzzsms-loader"
			/>
			<p>{__('Loading...', 'elitbuzz-sms')}</p>
		</div>
	);
};

export default Loader;
