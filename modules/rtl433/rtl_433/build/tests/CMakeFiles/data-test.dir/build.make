# CMAKE generated file: DO NOT EDIT!
# Generated by "Unix Makefiles" Generator, CMake Version 3.10

# Delete rule output on recipe failure.
.DELETE_ON_ERROR:


#=============================================================================
# Special targets provided by cmake.

# Disable implicit rules so canonical targets will work.
.SUFFIXES:


# Remove some rules from gmake that .SUFFIXES does not remove.
SUFFIXES =

.SUFFIXES: .hpux_make_needs_suffix_list


# Suppress display of executed commands.
$(VERBOSE).SILENT:


# A target that is always out of date.
cmake_force:

.PHONY : cmake_force

#=============================================================================
# Set environment variables for the build.

# The shell in which to execute make rules.
SHELL = /bin/sh

# The CMake executable.
CMAKE_COMMAND = /usr/bin/cmake

# The command to remove a file.
RM = /usr/bin/cmake -E remove -f

# Escaping for special characters.
EQUALS = =

# The top-level source directory on which CMake was run.
CMAKE_SOURCE_DIR = /var/www/modules/rtl433/rtl_433

# The top-level build directory on which CMake was run.
CMAKE_BINARY_DIR = /var/www/modules/rtl433/rtl_433/build

# Include any dependencies generated for this target.
include tests/CMakeFiles/data-test.dir/depend.make

# Include the progress variables for this target.
include tests/CMakeFiles/data-test.dir/progress.make

# Include the compile flags for this target's objects.
include tests/CMakeFiles/data-test.dir/flags.make

tests/CMakeFiles/data-test.dir/data-test.c.o: tests/CMakeFiles/data-test.dir/flags.make
tests/CMakeFiles/data-test.dir/data-test.c.o: ../tests/data-test.c
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green --progress-dir=/var/www/modules/rtl433/rtl_433/build/CMakeFiles --progress-num=$(CMAKE_PROGRESS_1) "Building C object tests/CMakeFiles/data-test.dir/data-test.c.o"
	cd /var/www/modules/rtl433/rtl_433/build/tests && /usr/bin/cc $(C_DEFINES) $(C_INCLUDES) $(C_FLAGS) -o CMakeFiles/data-test.dir/data-test.c.o   -c /var/www/modules/rtl433/rtl_433/tests/data-test.c

tests/CMakeFiles/data-test.dir/data-test.c.i: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Preprocessing C source to CMakeFiles/data-test.dir/data-test.c.i"
	cd /var/www/modules/rtl433/rtl_433/build/tests && /usr/bin/cc $(C_DEFINES) $(C_INCLUDES) $(C_FLAGS) -E /var/www/modules/rtl433/rtl_433/tests/data-test.c > CMakeFiles/data-test.dir/data-test.c.i

tests/CMakeFiles/data-test.dir/data-test.c.s: cmake_force
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green "Compiling C source to assembly CMakeFiles/data-test.dir/data-test.c.s"
	cd /var/www/modules/rtl433/rtl_433/build/tests && /usr/bin/cc $(C_DEFINES) $(C_INCLUDES) $(C_FLAGS) -S /var/www/modules/rtl433/rtl_433/tests/data-test.c -o CMakeFiles/data-test.dir/data-test.c.s

tests/CMakeFiles/data-test.dir/data-test.c.o.requires:

.PHONY : tests/CMakeFiles/data-test.dir/data-test.c.o.requires

tests/CMakeFiles/data-test.dir/data-test.c.o.provides: tests/CMakeFiles/data-test.dir/data-test.c.o.requires
	$(MAKE) -f tests/CMakeFiles/data-test.dir/build.make tests/CMakeFiles/data-test.dir/data-test.c.o.provides.build
.PHONY : tests/CMakeFiles/data-test.dir/data-test.c.o.provides

tests/CMakeFiles/data-test.dir/data-test.c.o.provides.build: tests/CMakeFiles/data-test.dir/data-test.c.o


# Object files for target data-test
data__test_OBJECTS = \
"CMakeFiles/data-test.dir/data-test.c.o"

# External object files for target data-test
data__test_EXTERNAL_OBJECTS =

tests/data-test: tests/CMakeFiles/data-test.dir/data-test.c.o
tests/data-test: tests/CMakeFiles/data-test.dir/build.make
tests/data-test: src/libdata.a
tests/data-test: tests/CMakeFiles/data-test.dir/link.txt
	@$(CMAKE_COMMAND) -E cmake_echo_color --switch=$(COLOR) --green --bold --progress-dir=/var/www/modules/rtl433/rtl_433/build/CMakeFiles --progress-num=$(CMAKE_PROGRESS_2) "Linking C executable data-test"
	cd /var/www/modules/rtl433/rtl_433/build/tests && $(CMAKE_COMMAND) -E cmake_link_script CMakeFiles/data-test.dir/link.txt --verbose=$(VERBOSE)

# Rule to build all files generated by this target.
tests/CMakeFiles/data-test.dir/build: tests/data-test

.PHONY : tests/CMakeFiles/data-test.dir/build

tests/CMakeFiles/data-test.dir/requires: tests/CMakeFiles/data-test.dir/data-test.c.o.requires

.PHONY : tests/CMakeFiles/data-test.dir/requires

tests/CMakeFiles/data-test.dir/clean:
	cd /var/www/modules/rtl433/rtl_433/build/tests && $(CMAKE_COMMAND) -P CMakeFiles/data-test.dir/cmake_clean.cmake
.PHONY : tests/CMakeFiles/data-test.dir/clean

tests/CMakeFiles/data-test.dir/depend:
	cd /var/www/modules/rtl433/rtl_433/build && $(CMAKE_COMMAND) -E cmake_depends "Unix Makefiles" /var/www/modules/rtl433/rtl_433 /var/www/modules/rtl433/rtl_433/tests /var/www/modules/rtl433/rtl_433/build /var/www/modules/rtl433/rtl_433/build/tests /var/www/modules/rtl433/rtl_433/build/tests/CMakeFiles/data-test.dir/DependInfo.cmake --color=$(COLOR)
.PHONY : tests/CMakeFiles/data-test.dir/depend
