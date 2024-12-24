import React from 'react';

/**
 * Render fields based on type
 * @param {*} field
 */
function Fields({ field, id, handleChange, value = '' }) {
	/**
	 * Render based on field type
	 * @param {*} field
	 */
	const renderByType = (field) => {
		const type = field.type;

		switch (type) {
			case 'text':
			case 'password':
				return (
					<input
						type={type}
						className="widefat"
						value={value} // Bind value properly to the state
						onChange={(e) => handleChange(e.target.value, id)} // Ensure value is updated
					/>
				);

			case 'checkbox':
				return (
					<>
						<input
							type="checkbox"
							className="widefat"
							id={id}
							checked={value === true} // Bind checkbox as a controlled component
							onChange={(e) => handleChange(e.target.checked, id)} // Update value based on checked status
						/>
						<label htmlFor={id}>{field.title}</label>
					</>
				);

			case 'select':
				const options = Object.entries(field.options);

				return (
					<select
						className="widefat"
						value={value} // Bind value properly
						onChange={(e) => handleChange(e.target.value, id)} // Update value based on selection
					>
						{options.map(([optionValue, optionLabel], index) => (
							<option key={index} value={optionValue}>
								{optionLabel}
							</option>
						))}
					</select>
				);

			case 'textarea':
				return (
					<textarea
						id={id}
						cols="30"
						rows="10"
						className="widefat"
						onChange={(e) => handleChange(e.target.value, id)} // Ensure the value is updated on change
						value={value || field?.default} // Use default if value is empty
					></textarea>
				);

			default:
				return '';
		}
	};

	return (
		<>
			<p className="label">{field?.title}</p>

			{renderByType(field)}

			{field?.description && (
				<p className="help-text">{field.description}</p>
			)}
		</>
	);
}

export default Fields;
