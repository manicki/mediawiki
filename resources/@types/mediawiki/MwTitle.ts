interface MwTitle {
	getNamespaceId(): number;
	getNamespacePrefix(): string;
	getName(): string;
	getNameText(): string;
	getExtension(): string|null;
	getDotExtension(): string;
	getMain(): string;
	getMainText(): string;
	getPrefixedDb(): string;
	getPrefixedText(): string;
	getRelativeText( namespace: number ): string;
	getFragment(): string|null;
	getUrl( params?: object ): string;
	isTalkPage(): boolean;
	getTalkPage(): MwTitle|null;
	getSubjectPage(): MwTitle|null;
	canHaveTalkPage(): boolean;
	exists(): boolean;

	toString(): string;
	toText(): string;

	phpCharToUpper( chr: string ): string;

	newFromText( title: string, namespace?: number ): MwTitle|null;
}

export default MwTitle;
