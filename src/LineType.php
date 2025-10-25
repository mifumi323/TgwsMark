<?php

namespace Mifumi323\TgwsMark;

enum LineType
{
    case Paragraph;
    case Header;
    case UnorderedList;
    case OrderedList;
    case Table;
    case CodeBlock;
}
