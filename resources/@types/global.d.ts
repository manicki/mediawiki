import { MediaWiki } from '@/@types/mediawiki';

declare global {
	const mw: MediaWiki;
	const $: JQueryStatic;
}
