import { mount } from 'fluid-primitives';
import { FileUpload } from 'fluid-primitives/file-upload';

mount('file-upload', ({ props }) => {
    const fileUpload = new FileUpload(props);
    fileUpload.init();
    return fileUpload;
});
