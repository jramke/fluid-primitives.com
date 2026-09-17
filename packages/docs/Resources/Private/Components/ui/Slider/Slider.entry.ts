import { mountAll } from 'fluid-primitives';
import { Slider } from 'fluid-primitives/slider';

mountAll('slider', ({ props }) => {
    const slider = new Slider(props);
    slider.init();
    return slider;
});
