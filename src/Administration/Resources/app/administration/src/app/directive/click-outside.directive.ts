// @ts-expect-error
import vClickOutside from 'v-click-outside';

const { Directive } = Shuwei;

// eslint-disable-next-line @typescript-eslint/no-unsafe-argument
Directive.register('click-outside', vClickOutside);
