type FieldType = 'text' | 'textarea' | 'checkbox' | 'dropdown' | 'number' | 'email' | 'url' | 'date' | 'file' | 'datetime-local'
export interface FieldOption {
	label: string
	value: string | number
}
export interface FieldDefinition {
	name: string // key for v-model
	label: string
	required?: boolean
	helptext?: string
	placeholder?: string
	fieldtype: FieldType
	options?: FieldOption[] // for dropdown
	autocomplete?: string // optional, for fields like email, name, etc.
	multiple?: boolean // for file input
	rows?: number
}
export interface Tile {
	id: string
	label: string
	desc: string
	cta: string
	icon: string
	link?: string
}
