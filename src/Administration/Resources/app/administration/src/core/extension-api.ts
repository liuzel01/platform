/**
 * @package admin
 *
 * @module core/extension-api
 */

import { handle } from '@58shuwei-ag/admin-extension-sdk/es/channel';
import { publishData, getPublishedDataSets } from './service/extension-api-data.service';

// eslint-disable-next-line sw-deprecation-rules/private-feature-declarations
export default {
    handle,
    publishData,
    getPublishedDataSets,
};
