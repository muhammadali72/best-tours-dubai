import React from 'react';
import {createRoot} from 'react-dom/client';
import Dashboard from './dashboard';
createRoot(document.getElementById('dashboard-root')!).render(<Dashboard email={JSON.parse(document.getElementById('cms-user')!.textContent!).email}/>);
