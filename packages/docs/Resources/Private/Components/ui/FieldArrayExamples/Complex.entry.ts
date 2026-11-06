import { mount } from 'fluid-primitives';
import { FieldArray } from 'fluid-primitives/field-array';

// The Select, DatePicker and Popover of an added row mount through their own entry files: `mountAll`
// picks up components whose markup arrives after the page loaded. Their content renders through
// `ui:portal`, which the clone moves to the end of the body.
mount('ui:fieldArray', 'complex-people', ({ props }) => new FieldArray(props).init());
