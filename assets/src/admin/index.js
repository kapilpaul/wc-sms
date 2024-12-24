import React from 'react';
import { createRoot } from 'react-dom/client';
import Routerview from './router/index';
import menuFix from './utils/admin-menu-fix';
import Header from './components/Header';
import { __ } from '@wordpress/i18n';
import './styles/style.scss';

const root = createRoot(document.getElementById('dc-elitbuzz-sms-app'));

const App = () => {
	return (
		<div className="wrap">
			<Header />
			<Routerview />
		</div>
	);
};

root.render(<App />);

// fix the admin menu for the slug "vue-app"
menuFix('dc-elitbuzz-sms-app');
