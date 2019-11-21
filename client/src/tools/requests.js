import { getUrl } from 'src/tools/url';
import { axiosInstance } from 'boot/axios';

export const getCategories = () => axiosInstance.get(getUrl('categories'));
