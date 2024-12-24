import React, { useState, useEffect } from 'react';

function Header() {
	return (
		<div className="elitbuzzsms_header_container">
			<div className="header_logo">
				<img src={getLogo()} alt="" />
			</div>
		</div>
	);
}

/**
 * get logo for header
 */
function getLogo() {
	return window.elitbuzzSMS.asset_url + '/images/elitbuzz.png';
}

export default Header;
