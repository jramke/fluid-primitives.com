import type { Preview } from '@andersundsehr/storybook-typo3';
import '../packages/docs/Resources/Private/css/main.css';
import './preview.css';

const preview: Preview = {
	// tags: ['autodocs'],
	parameters: {
		docs: {
			controls: { exclude: ['example_id'] },
		},
		layout: 'centered',
	},
};
export default preview;
