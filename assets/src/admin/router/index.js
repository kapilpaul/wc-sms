import React from 'react';
import { HashRouter, Route, Routes } from 'react-router-dom';
import Settings from '../Pages/settings';

const routes = [
	{
		path: '/settings',
		component: Settings,
	},
];

/**
 * Render all routes
 */
const Routerview = () => {
	return (
		<HashRouter>
			{routes.map((route, i) => (
				<Routes key={i}>
					<Route path={route.path} element={<route.component />} />
				</Routes>
			))}
		</HashRouter>
	);
};

export default Routerview;
