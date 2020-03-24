import MwLog from "@/@types/mediawiki/MwLog";
import MwTitle from "@/@types/mediawiki/MwTitle";

// Those are just stubs of incomplete "interfaces" to satisfy the compiler
export type MwUtilGetParamValue = ( param: string, url?: string ) => any|null;
export type MwUtilGetUrl = ( pageName?: string|null, params?: object ) => string;
export type MwConfigGet = ( selection?: string|string[], fallback?: any) => any;

export interface MediaWiki {
	log: MwLog,
	Title: MwTitle,

	config: {
		get: MwConfigGet;
	},
	util: {
		getParamValue: MwUtilGetParamValue;
		getUrl: MwUtilGetUrl;
	}
}
