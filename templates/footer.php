<script type="text/javascript" src="/public/js/jquery-3.4.1.min.js"></script>
	<script type="text/javascript" src="/public/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="/public/js/mdb.min.js"></script>
	<!-- сортировка таблицы -->
	<script type="text/javascript">
		var sort_case_sensitive = false;

		function _sort(a, b) {
			var a = a[0];
			var b = b[0];
			var _a = (a + '').replace(/,/, '.');
			var _b = (b + '').replace(/,/, '.');
			if (parseFloat(_a) && parseFloat(_b)) return sort_numbers(parseFloat(_a), parseFloat(_b));
			else if (!sort_case_sensitive) return sort_insensitive(a, b);
			else return sort_sensitive(a, b);
		}

		function sort_numbers(a, b) {
			return a - b;
		}

		function sort_insensitive(a, b) {
			var anew = a.toLowerCase();
			var bnew = b.toLowerCase();
			if (anew < bnew) return -1;
			if (anew > bnew) return 1;
			return 0;
		}

		function sort_sensitive(a, b) {
			if (a < b) return -1;
			if (a > b) return 1;
			return 0;
		}

		function getConcatenedTextContent(node) {
			var _result = "";

			if (node == null) {
				return _result;
			}
			var childrens = node.childNodes;
			var i = 0;

			while (i < childrens.length) {
				var child = childrens.item(i);
				switch (child.nodeType) {
					case 1:
					case 5:
						_result += getConcatenedTextContent(child);
						break;
					case 3:
					case 2:
					case 4:
						_result += child.nodeValue;
						break;
					break;
				}
				i++;
			}
			return _result;
		}

		function sort(e) {
			var el = window.event ? window.event.srcElement : e.currentTarget;
			while (el.tagName.toLowerCase() != "td") el = el.parentNode;
			var a = new Array();
			var name = el.lastChild.nodeValue;
			var dad = el.parentNode;
			var table = dad.parentNode.parentNode;
			var up = table.up;
			var node, arrow, curcol;
			for (var i = 0; (node = dad.getElementsByTagName("td").item(i)); i++) {
					if (node.lastChild.nodeValue == name) {
							curcol = i;
							if (node.className == "curcol") {
									arrow = node.firstChild;
									table.up = Number(!up);
							} else {
									node.className = "curcol";
									arrow = node.insertBefore(document.createElement("img"),node.firstChild);
									table.up = 0;
							}
							arrow.alt = "";
					} else {
							if (node.className == "curcol") {
									node.className = "";
									if (node.firstChild) node.removeChild(node.firstChild);
							}
					}
			}
			var tbody = table.getElementsByTagName("tbody").item(0);
			for (var i = 0; (node = tbody.getElementsByTagName("tr").item(i)); i++) {
					a[i] = new Array();
					a[i][0] = getConcatenedTextContent(node.getElementsByTagName("td").item(curcol));
					a[i][1] = getConcatenedTextContent(node.getElementsByTagName("td").item(1));
					a[i][2] = getConcatenedTextContent(node.getElementsByTagName("td").item(0));
					a[i][3] = node;
			}
			a.sort(_sort);
			if (table.up) a.reverse();
			for (var i = 0; i < a.length; i++) {
					tbody.appendChild(a[i][3]);
			}
		}

		function init(e) {
			if (!document.getElementsByTagName) return;

			for (var j = 0; (thead = document.getElementsByTagName("thead").item(j)); j++) {
				var node;
				for (var i = 0; (node = thead.getElementsByTagName("td").item(i)); i++) {
					if (node.addEventListener) node.addEventListener("click", sort, false);
					else if (node.attachEvent) node.attachEvent("onclick", sort);
					node.title = "Нажмите на заголовок, чтобы отсортировать колонку";
				}
				thead.parentNode.up = 0;
				
				if (typeof (initial_sort_id) != "undefined") {
					td_for_event = thead.getElementsByTagName("td").item(initial_sort_id);
					if (document.createEvent) {
						var evt = document.createEvent("MouseEvents");
						evt.initMouseEvent("click", false, false, window, 1, 0, 0, 0, 0, 0, 0, 0, 0, 1, td_for_event);
						td_for_event.dispatchEvent(evt);
					} else if (td_for_event.fireEvent) td_for_event.fireEvent("onclick");
					if (typeof (initial_sort_up) != "undefined" && initial_sort_up) {
						if (td_for_event.dispatchEvent) td_for_event.dispatchEvent(evt);
						else if (td_for_event.fireEvent) td_for_event.fireEvent("onclick");
					}
				}
			}
		}

		var root = window.addEventListener || window.attachEvent ? window : document.addEventListener ? document : null;
		if (root) {
			if (root.addEventListener) root.addEventListener("load", init, false);
			else if (root.attachEvent) root.attachEvent("onload", init);
		}
	</script>
</body>
</html>