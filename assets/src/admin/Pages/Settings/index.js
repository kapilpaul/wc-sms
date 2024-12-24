import React, { useEffect, useState } from 'react';
import { __ } from '@wordpress/i18n';
import apiFetch from '@wordpress/api-fetch';
import { API } from '../../../constants';
import Fields from '../../components/fields';
import { Button } from '@wordpress/components';
import { ToastContainer, toast } from 'react-toastify';
import './styles.scss';

const Settings = () => {
	const [settings, setSettings] = useState({});
	const [isSubmitted, setIsSubmitted] = useState(false);

	useEffect(() => {
		apiFetch({
			path: API.v1.settings,
			parse: false,
		})
			.then((resp) => {
				resp.json().then((body) => {
					setSettings(body);
				});
			})
			.catch((err) => {
				console.log(err.message);
			});
	}, []); // Ensure the effect runs only once on mount

	const handleChange = (inputVal, id) => {
		if (inputVal === undefined) return;

		// Update the specific input value based on the ID, keeping all other settings intact
		setSettings((prevSettings) => ({
			...prevSettings,
			[id]: {
				...prevSettings[id], // Keep previous settings for this specific ID
				value: inputVal, // Update the value for this specific ID
			},
		}));
	};

	const handleSubmit = () => {
		setIsSubmitted(true);

		apiFetch({
			path: API.v1.settings,
			method: 'POST',
			data: { data: settings },
		})
			.then((resp) => {
				setIsSubmitted(false);
				setSettings(resp);

				toast.success('Wow so easy!');
			})
			.catch((err) => {
				setIsSubmitted(false);
				toast.error(err.data.status + ' : ' + err.message);
			});
	};

	return (
		<div className="settings_page">
			<h1>{__('Settings', 'elitbuzz-sms')}</h1>

			<ToastContainer
				position="top-right"
				autoClose={5000}
				hideProgressBar={false}
				newestOnTop={false}
				closeOnClick={false}
				rtl={false}
				pauseOnFocusLoss
				draggable
				pauseOnHover
				theme="colored"
			/>

			{Object.keys(settings).map((key) => {
				return (
					<div key={key} className="single_settings_field">
						<Fields
							field={settings[key]}
							id={key}
							handleChange={handleChange}
							value={settings[key]?.value}
						/>
					</div>
				);
			})}

			<Button
				type="submit"
				className="save_btn"
				variant="primary"
				isBusy={isSubmitted}
				disabled={isSubmitted}
				onClick={() => handleSubmit()}
			>
				Save
			</Button>
		</div>
	);
};

export default Settings;
