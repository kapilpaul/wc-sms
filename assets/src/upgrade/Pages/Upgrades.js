import React, { useState, useEffect } from 'react';
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { toast } from 'react-toastify';
import { API } from '../../constants';
import '../../admin/styles/react-toastify.scss';

/**
 * configure the toast
 */
toast.configure({
	position: 'top-right',
	autoClose: 5000,
	closeOnClick: false,
	pauseOnHover: false,
	draggable: false,
	closeButton: false,
	style: {
		top: '3em',
	},
});

function Upgrades() {
	const [isSubmitted, setIsSubmitted] = useState(false);

	/**
	 * Handle update from here.
	 */
	const handleUpdate = () => {
		setIsSubmitted(true);

		apiFetch({
			path: API.v1.upgrade,
			method: 'POST',
			data: {},
		})
			.then((resp) => {
				setIsSubmitted(false);
				toast.success(__('Updated Successfully!', 'elitbuzz-sms'));

				hideNotice();
			})
			.catch((err) => {
				setIsSubmitted(false);
				toast.error(err.data.status + ' : ' + err.message);
			});
	};

	/**
	 * Hide notice container
	 */
	const hideNotice = () => {
		let noticeContainer = document.querySelector(
			'.elitbuzz-sms-notice-info'
		);

		noticeContainer.classList.add('elitbuzz-sms-notice-info-hide');
	};

	return (
		<div id="elitbuzz-sms-upgrade-notice">
			<div id="elitbuzz-sms-upgrade-notice-icon">
				<div id="elitbuzz-sms-upgrade-notice-message">
					<div id="elitbuzz-sms-upgrade-notice-title">
						<p>
							<strong>
								{__(
									'elitbuzzsms Data Update Required',
									'elitbuzz-sms'
								)}
							</strong>
						</p>
					</div>
					<div id="elitbuzz-sms-upgrade-notice-content">
						<p>
							{__(
								'We need to update your install to the latest version',
								'elitbuzz-sms'
							)}
						</p>
					</div>

					<Button
						type="submit"
						className="wc-update-now bg-elitbuzzsms text-white"
						onClick={() => handleUpdate()}
						isBusy={isSubmitted}
						disabled={isSubmitted}
					>
						{isSubmitted
							? __('Updating', 'elitbuzz-sms')
							: __('Update', 'elitbuzz-sms')}
					</Button>
				</div>
			</div>
		</div>
	);
}

export default Upgrades;
