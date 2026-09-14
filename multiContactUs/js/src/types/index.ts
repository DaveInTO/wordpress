type FieldType =
	| 'text'
	| 'textarea'
	| 'checkbox'
	| 'checkbox-group'
	| 'radio'
	| 'dropdown'
	| 'number'
	| 'email'
	| 'tel'
	| 'url'
	| 'date'
	| 'file'
	| 'datetime-local'
	| 'section'

export interface FieldOption {
	label: string
	value: string | number
}

/** Show the field only when another field's value matches. */
export interface FieldCondition {
	field: string
	equals: string | number | Array<string | number>
}

export interface FieldDefinition {
	name: string // key for v-model; for 'section' this is only a render key
	label: string
	required?: boolean
	helptext?: string
	placeholder?: string
	fieldtype: FieldType
	options?: FieldOption[] // for dropdown, radio, checkbox-group
	autocomplete?: string // optional, for fields like email, name, etc.
	multiple?: boolean // for file input
	rows?: number
	min?: number
	max?: number
	/** checkbox-group only: separator used when joining selected labels for submission. */
	joinWith?: string
	/** render this field only when the condition is met */
	showIf?: FieldCondition
}

export interface FormDefinition {
	title: string
	fields: FieldDefinition[]
	submitLabel?: string
	successMessage?: string
}

export interface Tile {
	id: string
	label: string
	desc: string
	cta: string
	icon: string
	link?: string
}
