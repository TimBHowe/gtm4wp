jQuery(function ($) {
	// $(document).bind("ajaxComplete", function (evt, jqXhr, opts) {
	// 	console.log("fire", evt, jqXhr, opts);
	// });
	$(document).ajaxComplete(function (event, request, settings) {

		// Get the data from the edd-ajax.js ajax request.
		const data = Object.fromEntries(new URLSearchParams(settings.data));
		console.log(data);

		// if (string.includes(substring)) {}
		throw new Error("Kill JS");
	});
});
