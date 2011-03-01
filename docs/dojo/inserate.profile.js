/* Copy this file to "util/buildscripts/profiles/inserate.profile.js"
   Create dojo.js from source and copy to "/public/js/dojo/"
   Execute on shell: ./build.sh profile=inserate action=release
*/
dependencies = {
	stripConsole: "normal",

	layers: [
		{
			name: "dojo.js",
			dependencies: [
                "dojo.cache",
                "dojo.parser",
                "dojo.date",
                "dojo.cldr",
                "dojo.number",
                "dojo.currency",
                "dojox.gfx",
                "dojox.fx",
                "dojox.gfx.svg",
                "dojo.nls.dojo_de",
                "dijit.layout.ContentPane",
                "dijit.layout.TabContainer",
                "dijit.form.Form",
                "dijit.form.ValidationTextBox",
                "dijit.form.FilteringSelect",
                "dijit.form.TextBox",
                "dijit.form.DateTextBox",
                "dijit.form.CurrencyTextBox",
                "dijit.form.CheckBox",
                "dijit.form.RadioButton",
                "dijit.form.NumberSpinner",
                "dijit.form.SubmitButton",
                "dijit.form.PasswordTextBox",
                "dijit.form.SimpleTextarea",
                "dojox.charting.Chart2D",
                "dojox.charting.widget.Legend",
                "dojox.charting.action2d.Tooltip"
			]
		},
	],

	prefixes: [	]
}
